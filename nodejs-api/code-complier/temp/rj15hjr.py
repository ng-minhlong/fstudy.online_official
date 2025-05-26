class Vector2D:
    def __init__(self, vec):
        self.vec = vec
        self.row = 0
        self.col = 0

    def next(self):
        val = self.vec[self.row][self.col]
        self.col += 1
        return val

    def hasNext(self):
        while self.row < len(self.vec):
            if self.col < len(self.vec[self.row]):
                return True
            self.row += 1
            self.col = 0
        return False